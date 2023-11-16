import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

class AnyOf implements OvO {
  final Iterable<OvO> schemas;
  final String? message;

  const AnyOf(this.schemas, [this.message]);

  @override
  Future handle(Context context, data) async {
    final exceptions = <Exception>[];
    for (final schema in schemas) {
      try {
        return await schema.handle(context, data);
      } on Exception catch (e) {
        exceptions.add(e);
      }
    }

    throw OvOException(
      code: #any_of,
      message: message ?? 'Any schema must be valid',
      exceptions: exceptions,
    );
  }
}
