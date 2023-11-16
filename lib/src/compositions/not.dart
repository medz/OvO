import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

class Not<T> implements OvO {
  final OvO<T> schema;
  final String? message;

  const Not(this.schema, [this.message]);

  @override
  Future<T> handle(Context context, data) async {
    try {
      await schema.handle(context, data);
    } catch (_) {
      return data;
    }

    throw OvOException(
      code: #not,
      message: message ?? 'Schema must be invalid',
    );
  }
}
