import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

class OneOf implements OvO {
  final Iterable<OvO> schemas;
  final String? message;

  const OneOf(this.schemas, [this.message]);

  @override
  Future handle(Context context, data) async {
    final results = <dynamic>[];
    final exceptions = <Exception>[];

    for (final schema in schemas) {
      try {
        results.add(await schema.handle(context, data));
      } on Exception catch (e) {
        exceptions.add(e);
      }
    }

    if (results.length == 1) return results.single;

    throw OvOException(
      code: #one_of,
      message: message ?? 'One schema must be valid',
      exceptions: exceptions,
    );
  }
}
